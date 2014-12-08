<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
 * Classe de mapeamento da tabela tcemg.registros_arquivo_programa
 * Data de Criação: 11/03/2014
 * 
 * @author Analista      : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGProcessoAdesaoRegistroPrecos.class.php 59719 2014-09-08 15:00:53Z franver $
 * $Revision: 59719 $
 * $Author: franver $
 * $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
 * 
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGProcessoAdesaoRegistroPrecos extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function TTCEMGProcessoAdesaoRegistroPrecos()
    {
        parent::Persistente();

        $this->setTabela('tcemg.processo_adesao_registro_precos');
        $this->setComplementoChave('cod_entidade, numero_processo_adesao, exercicio_adesao');

        $this->addCampo('cod_entidade'                     , 'integer' , true  , '' , true  , false );
        $this->addCampo('numero_processo_adesao'           , 'integer' , true  , '' , true  , false );
        $this->addCampo('exercicio_adesao'                 , 'varchar' , true  , '4', true  , false );
        $this->addCampo('data_abertura_processo_adesao'    , 'date'    , true  , '' , false , false );
        $this->addCampo('numcgm'                           , 'integer' , true  , '' , false , true  );
        $this->addCampo('exercicio_licitacao'              , 'varchar' , true  , '4', false , false );
        $this->addCampo('numero_processo_licitacao'        , 'integer' , true  , '' , false , false );
        $this->addCampo('codigo_modalidade_licitacao'      , 'integer' , true  , '' , false , false );
        $this->addCampo('numero_modalidade'                , 'integer' , true  , '' , false , false );
        $this->addCampo('data_ata_registro_preco'          , 'date'    , true  , '' , false , false );
        $this->addCampo('data_ata_registro_preco_validade' , 'date'    , true  , '' , false , false );
        $this->addCampo('natureza_procedimento'            , 'integer' , true  , '' , false , false );
        $this->addCampo('data_publicacao_aviso_intencao'   , 'date'    , false , '' , false , false );
        $this->addCampo('objeto_adesao'                    , 'text'    , true  , '' , false , false );
        $this->addCampo('cgm_responsavel'                  , 'integer' , true  , '' , false , true  );
        $this->addCampo('desconto_tabela'                  , 'integer' , true  , '' , false , false );
        $this->addCampo('processo_lote'                    , 'integer' , true  , '' , false , false );
        $this->addCampo('exercicio'                        , 'varchar' , true  , '4', false , true  );
        $this->addCampo('num_unidade'                      , 'integer' , true  , '' , false , true  );
        $this->addCampo('num_orgao'                        , 'integer' , true  , '' , false , true  );
    }
    
    public function recuperaProcesso(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaProcesso($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }
    
    public function montaRecuperaProcesso()
    {
        $stSql = "
            SELECT cod_entidade
                 , LPAD(numero_processo_adesao::VARCHAR, 12, '0') || '/'|| exercicio_adesao AS codigo_processo_adesao
                 , TO_CHAR(data_abertura_processo_adesao,'dd/mm/yyyy') AS data_abertura_processo_adesao
                 , sw_cgm.numcgm  AS numcgm_orgao_gerenciador
                 , sw_cgm.nom_cgm AS nomcgm_orgao_gerenciador
                 , numero_processo_licitacao
                 , exercicio_licitacao
                 , codigo_modalidade_licitacao
                 , numero_modalidade
                 , TO_CHAR(data_ata_registro_preco,'dd/mm/yyyy') AS data_ata_registro_preco
                 , TO_CHAR(data_ata_registro_preco_validade,'dd/mm/yyyy') AS data_ata_registro_preco_validade
                 , natureza_procedimento
                 , TO_CHAR(data_publicacao_aviso_intencao,'dd/mm/yyyy') AS data_publicacao_aviso_intencao
                 , objeto_adesao
                 , sw_cgm_responsavel.numcgm  AS numcgm_responsavel
                 , sw_cgm_responsavel.nom_cgm AS nomcgm_responsavel
                 , desconto_tabela
                 , processo_lote
                 , exercicio
                 , LPAD(num_orgao::VARCHAR, 2, '0') || '.' || LPAD(num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria

              FROM tcemg.processo_adesao_registro_precos
             
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = processo_adesao_registro_precos.numcgm

        INNER JOIN sw_cgm as sw_cgm_responsavel
                ON sw_cgm_responsavel.numcgm = processo_adesao_registro_precos.cgm_responsavel

             WHERE processo_adesao_registro_precos.exercicio_adesao       = '".$this->getDado('exercicio_adesao')."'
               AND processo_adesao_registro_precos.numero_processo_adesao = ".$this->getDado('numero_processo_adesao')."
               AND processo_adesao_registro_precos.cod_entidade           = ".$this->getDado('cod_entidade') ;

        return $stSql;
    }

    public function recuperaListaProcesso(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaProcesso($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }
    
    public function montaRecuperaListaProcesso()
    {
        $stSql = "
            SELECT cod_entidade
                 , LPAD(numero_processo_adesao::VARCHAR, 12, '0') || '/'|| exercicio_adesao AS codigo_processo_adesao
                 , numero_processo_adesao
                 , exercicio_adesao
                 , TO_CHAR(data_abertura_processo_adesao,'dd/mm/yyyy') AS data_abertura_processo_adesao
                 , LPAD(numero_processo_licitacao::VARCHAR, 15, '0') || '/' || exercicio_licitacao AS codigo_processo_licitacao 
                 , CASE WHEN codigo_modalidade_licitacao = 1 THEN 'Concorrência' ELSE 'Pregão' END AS modalidade 
                 , numero_modalidade
                 , natureza_procedimento

              FROM tcemg.processo_adesao_registro_precos

             WHERE cod_entidade = ".$this->getDado('cod_entidade');

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO10()
    {
        $stSql = "
        
             SELECT  parp.cod_entidade::VARCHAR||parp.numero_processo_adesao::VARCHAR||parp.exercicio_adesao::VARCHAR AS chave10
                  ,  10 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  TO_CHAR(parp.data_abertura_processo_adesao, 'ddmmyyyy') AS data_abertura_processo_adesao
                  ,  sw_cgm.nom_cgm AS nome_orgao_gerenciador
                  ,  parp.exercicio_licitacao
                  ,  parp.numero_processo_licitacao
                  ,  parp.codigo_modalidade_licitacao
                  ,  parp.numero_modalidade
                  ,  TO_CHAR(parp.data_ata_registro_preco, 'ddmmyyyy') AS data_ata_registro_preco
                  ,  TO_CHAR(parp.data_ata_registro_preco_validade, 'ddmmyyyy') AS data_ata_registro_preco_validade
                  ,  parp.natureza_procedimento
                  ,  TO_CHAR(parp.data_publicacao_aviso_intencao, 'ddmmyyyy') AS data_publicacao_aviso_intencao
                  ,  parp.objeto_adesao
                  ,  (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE sw_cgm_pessoa_fisica.numcgm = parp.cgm_responsavel) AS cpf_responsavel
                  ,  parp.desconto_tabela
                  ,  parp.processo_lote

              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = parp.numcgm
            
             WHERE  1=1 ";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        if ($this->getDado('mes_referencia')) {
            $stSql .= " AND EXTRACT (MONTH FROM parp.data_abertura_processo_adesao) = ".$this->getDado('mes_referencia'); 
        }
        
        return $stSql;
    }

    public function recuperaExportacaoREGADESAO11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO11()
    {
        $stSql = "
        
             SELECT  lrp.cod_entidade::VARCHAR||lrp.numero_processo_adesao::VARCHAR||lrp.exercicio_adesao::VARCHAR AS chave11
                  ,  11 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  lrp.cod_lote
                  ,  lrp.descricao_lote

              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  tcemg.lote_registro_precos lrp
                ON  lrp.cod_entidade = parp.cod_entidade
               AND  lrp.numero_processo_adesao = parp.numero_processo_adesao
               AND  lrp.exercicio_adesao = parp.exercicio_adesao
             
             WHERE  1=1 ";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO12(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO12()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_processo_adesao::VARCHAR||irp.exercicio_adesao::VARCHAR AS chave12
                  ,  12 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  irp.cod_item
                  ,  irp.num_item

              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_processo_adesao = parp.numero_processo_adesao
               AND  irp.exercicio_adesao = parp.exercicio_adesao
             
             WHERE  1=1 ";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO13(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO13()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_processo_adesao::VARCHAR||irp.exercicio_adesao::VARCHAR AS chave13
                  ,  13 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  irp.cod_item
                  ,  irp.cod_lote

              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_processo_adesao = parp.numero_processo_adesao
               AND  irp.exercicio_adesao = parp.exercicio_adesao
             
             WHERE  1=1 ";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO14(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO14().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO14()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_processo_adesao::VARCHAR||irp.exercicio_adesao::VARCHAR AS chave14
                  ,  14 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  irp.cod_lote
                  ,  irp.cod_item
                  ,  TO_CHAR(irp.data_cotacao,'ddmmyyyy') AS data_cotacao
                  ,  REPLACE(irp.vl_cotacao_preco_unitario::VARCHAR,'.',',') AS vl_cotacao_preco_unitario
                  ,  REPLACE(irp.quantidade_cotacao::VARCHAR,'.',',') AS quantidade_cotacao
                  
              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_processo_adesao = parp.numero_processo_adesao
               AND  irp.exercicio_adesao = parp.exercicio_adesao
             
             WHERE  1=1 ";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO15(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO15().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO15()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_processo_adesao::VARCHAR||irp.exercicio_adesao::VARCHAR AS chave15
                  ,  15 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  irp.cod_lote
                  ,  irp.cod_item
                  ,  REPLACE(irp.preco_unitario::VARCHAR,'.',',') AS preco_unitario
                  ,  REPLACE(irp.quantidade_licitada::VARCHAR,'.',',') AS quantidade_licitada
                  ,  REPLACE(irp.quantidade_aderida::VARCHAR,'.',',') AS quantidade_aderida
                  ,  CASE WHEN sw_cgm.cod_pais <> 1 THEN 3
                          WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                          ELSE 2 END AS tipo_documento                 
                  ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf                        
                          ELSE sw_cgm_pessoa_juridica.cnpj END AS nro_documento
                  
              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_processo_adesao = parp.numero_processo_adesao
               AND  irp.exercicio_adesao = parp.exercicio_adesao
             
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = irp.cgm_vencedor
        
         LEFT JOIN sw_cgm_pessoa_fisica   
                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm               

         LEFT JOIN sw_cgm_pessoa_juridica
                ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

             WHERE  1=1 ";

        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO20(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO20().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO20()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_processo_adesao::VARCHAR||irp.exercicio_adesao::VARCHAR AS chave20
                  ,  20 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  LPAD(LPAD(num_orgao::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_sub
                  ,  parp.numero_processo_adesao  
                  ,  parp.exercicio_adesao
                  ,  irp.cod_lote
                  ,  irp.cod_item
                  ,  irp.percentual_desconto AS percentual_desconto_item
                  ,  lrp.percentual_desconto_lote AS percentual_desconto_lote
                  ,  CASE WHEN sw_cgm.cod_pais <> 1 THEN 3
                          WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                          ELSE 2 END AS tipo_documento                 
                  ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf                        
                          ELSE sw_cgm_pessoa_juridica.cnpj END AS nro_documento
                  
              FROM  tcemg.processo_adesao_registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_processo_adesao = parp.numero_processo_adesao
               AND  irp.exercicio_adesao = parp.exercicio_adesao

         LEFT JOIN  tcemg.lote_registro_precos lrp
                ON  lrp.cod_entidade = irp.cod_entidade
               AND  lrp.numero_processo_adesao = irp.numero_processo_adesao
               AND  lrp.exercicio_adesao = irp.exercicio_adesao
               AND  lrp.cod_lote = irp.cod_lote

        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = irp.cgm_vencedor
        
         LEFT JOIN sw_cgm_pessoa_fisica   
                ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm               

         LEFT JOIN sw_cgm_pessoa_juridica
                ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

             WHERE  1=1 ";

        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        return $stSql;
    }

public function __destruct(){}
    
}

?>