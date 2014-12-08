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
	* Classe de mapeamento da tabela tcemg.contrato
	* Data de Criação   : 06/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGContrato.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGContrato extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGContrato()
    {
        parent::Persistente();
        $this->setTabela('tcemg.contrato');
        
        $this->setCampoCod('cod_contrato');
        $this->setComplementoChave('exercicio,cod_entidade');
        
        $this->AddCampo( 'cod_contrato'             , 'integer' , true  , ''    , true  , false );
        $this->AddCampo( 'cod_entidade'             , 'integer' , true  , ''    , true  , true  );
        $this->AddCampo( 'num_orgao'                , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'num_unidade'              , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'nro_contrato'             , 'integer' , true  , ''    , false , false );
        $this->AddCampo( 'exercicio'                , 'char'    , true  , '4'   , true  , true  );
        $this->AddCampo( 'data_assinatura'          , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'cod_modalidade_licitacao' , 'char'    , true  , '1'   , false , true  );
        $this->AddCampo( 'cod_entidade_modalidade'  , 'integer' , false , ''    , false , false );
        $this->AddCampo( 'num_orgao_modalidade'     , 'integer' , false , ''    , false , false );
        $this->AddCampo( 'num_unidade_modalidade'   , 'integer' , false , ''    , false , false );
        $this->AddCampo( 'nro_processo'             , 'numeric' , false , '5,0' , false , false );
        $this->AddCampo( 'exercicio_processo'       , 'char'    , false , '4'   , false , false );
        $this->AddCampo( 'cod_tipo_processo'        , 'char'    , false , '1'   , false , true  );
        $this->AddCampo( 'cod_objeto'               , 'char'    , true  , '1'   , false , true  );
        $this->AddCampo( 'objeto_contrato'          , 'varchar' , true  , '500' , false , false );
        $this->AddCampo( 'cod_instrumento'          , 'char'    , true  , '1'   , false , true  );
        $this->AddCampo( 'data_inicio'              , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'data_final'               , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'vl_contrato'              , 'numeric' , true  , '14,2', false , false );
        $this->AddCampo( 'fornecimento'             , 'varchar' , false , '50'  , false , false );
        $this->AddCampo( 'pagamento'                , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'execucao'                 , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'multa'                    , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'multa_inadimplemento'     , 'varchar' , false , '100' , false , false );
        $this->AddCampo( 'cod_garantia'             , 'char'    , false , '1'   , false , true  );
        $this->AddCampo( 'numcgm_contratante'       , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'data_publicacao'          , 'date'    , true  , ''    , false , false );
        $this->AddCampo( 'numcgm_publicidade'       , 'integer' , true  , ''    , false , true  );
        $this->AddCampo( 'cgm_signatario'           , 'integer' , true  , ''    , false , true  );
    }

    function recuperaProximoContrato(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        $stSql = $this->montaRecuperaProximoContrato();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
        
        return $obErro;
    }

    function montaRecuperaProximoContrato()
    {
        $stSql  = " SELECT max(cod_contrato) + 1 as cod_contrato    \n";
        $stSql .= " FROM tcemg.contrato                             \n";

        return $stSql;
    }
    
    public function recuperaContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaContrato()
    {
        $stSql = "SELECT 
            (''||contrato.cod_entidade||' - '||(SELECT sw_cgm.nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm=entidade.numcgm))
            AS nom_entidade,
            Modalidade.descricao AS st_modalidade,
            Natureza.descricao AS st_natureza,
            Instrumento.descricao AS st_instrumento,
            contrato.*
            FROM tcemg.contrato
            INNER JOIN orcamento.entidade
            ON entidade.exercicio=contrato.exercicio
            AND entidade.cod_entidade=contrato.cod_entidade
            INNER JOIN tcemg.contrato_modalidade_licitacao AS Modalidade
            ON Modalidade.cod_modalidade_licitacao=contrato.cod_modalidade_licitacao
            INNER JOIN tcemg.contrato_objeto AS Natureza
            ON Natureza.cod_objeto=contrato.cod_objeto
            INNER JOIN tcemg.contrato_instrumento AS Instrumento 
            ON Instrumento.cod_instrumento=contrato.cod_instrumento
        ";

        return $stSql;
    }
    
    public function recuperaContratoRescisao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContratoRescisao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaContratoRescisao()
    {
        $stSql = "SELECT 
	    contrato.cod_entidade,
	    contrato.nro_contrato,
	    contrato.exercicio,
	    contrato.vl_contrato,
	    contrato.objeto_contrato,
	    contrato.cod_contrato,
	    to_char(contrato.data_assinatura, 'dd/mm/yyyy') AS data_assinatura,
	    to_char(contrato.data_inicio, 'dd/mm/yyyy') AS data_inicio,
	    to_char(contrato.data_final, 'dd/mm/yyyy') AS data_final,
	    to_char(contrato_rescisao.data_rescisao, 'dd/mm/yyyy') AS data_rescisao,
	    contrato_rescisao.valor_rescisao
            FROM tcemg.contrato
	    LEFT JOIN tcemg.contrato_rescisao
	    ON contrato_rescisao.cod_contrato=contrato.cod_contrato
	    AND contrato_rescisao.exercicio=contrato.exercicio
	    AND contrato_rescisao.cod_entidade=contrato.cod_entidade
        ";

        return $stSql;
    }
    
    

    function alteraContrato($boTransacao = false)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
    
        $stSql = $this->montaAlteraContrato();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaDML( $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaAlteraContrato()
    {
        $cod_entidade_modalidade    = ($this->getDado( 'cod_entidade_modalidade')!=''   ) ? $this->getDado('cod_entidade_modalidade')     : "NULL";
        $cod_tipo_processo          = ($this->getDado( 'cod_tipo_processo')!=''         ) ? $this->getDado('cod_tipo_processo')           : "NULL";
        $cod_garantia               = ($this->getDado( 'cod_garantia')!=''              ) ? $this->getDado('cod_garantia')                : "NULL";
        $num_orgao_modalidade       = ($this->getDado( 'num_orgao_modalidade')!=''      ) ? $this->getDado('num_orgao_modalidade')        : "NULL";
        $num_unidade_modalidade     = ($this->getDado( 'num_unidade_modalidade')!=''    ) ? $this->getDado('num_unidade_modalidade')      : "NULL";
        $nro_processo               = ($this->getDado( 'nro_processo')!=''              ) ? $this->getDado('nro_processo')                : "NULL";
        $exercicio_processo         = ($this->getDado( 'exercicio_processo')!=''        ) ? "'".$this->getDado('exercicio_processo')."'"  : "NULL";
        $fornecimento               = ($this->getDado( 'fornecimento')!=''              ) ? "'".$this->getDado('fornecimento')."'"        : "NULL";
        $pagamento                  = ($this->getDado( 'pagamento')!=''                 ) ? "'".$this->getDado('pagamento')."'"           : "NULL";
        $execucao                   = ($this->getDado( 'execucao')!=''                  ) ? "'".$this->getDado('execucao')."'"            : "NULL";
        $multa                      = ($this->getDado( 'multa')!=''                     ) ? "'".$this->getDado('multa')."'"               : "NULL";
        $multa_inadimplemento       = ($this->getDado( 'multa_inadimplemento')!=''      ) ? "'".$this->getDado('multa_inadimplemento')."'": "NULL";
        
        
        $stSql  = " UPDATE tcemg.contrato\n";
        $stSql .= " SET cod_entidade_modalidade = ".$cod_entidade_modalidade.", \n";
        $stSql .= " cod_tipo_processo = ".$cod_tipo_processo.",                 \n";
        $stSql .= " cod_garantia = ".$cod_garantia.",                           \n";
        $stSql .= " num_orgao_modalidade = ".$num_orgao_modalidade.",           \n";
        $stSql .= " num_unidade_modalidade = ".$num_unidade_modalidade.",       \n";
        $stSql .= " nro_processo = ".$nro_processo.",                           \n";
        $stSql .= " exercicio_processo = ".$exercicio_processo.",               \n";
        $stSql .= " fornecimento = ".$fornecimento.",                           \n";
        $stSql .= " pagamento = ".$pagamento.",                                 \n";
        $stSql .= " execucao = ".$execucao.",                                   \n";
        $stSql .= " multa = ".$multa.",                                         \n";
        $stSql .= " multa_inadimplemento = ".$multa_inadimplemento."            \n";
        $stSql .= " WHERE cod_contrato = ".$this->getDado('cod_contrato')."     \n";
        $stSql .= " AND exercicio = '".$this->getDado('exercicio')."'           \n";
        $stSql .= " AND cod_entidade = '".$this->getDado('cod_entidade')."'     \n";
        
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato10()
    {
        $stSql  = " SELECT 10 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    (SELECT valor FROM administracao.configuracao_entidade
                                   WHERE exercicio=TC.exercicio
                                   AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                   AND cod_entidade=TC.cod_entidade) 
                    AS codOrgao,
                    LPAD((LPAD(''||TC.num_orgao,2, '0')||LPAD(''||TC.num_unidade,2, '0')), 5, '0') AS codUnidadeSub,
                    TC.nro_contrato AS nroContrato,
                    TC.exercicio AS exercicioContrato,
                    to_char(TC.data_assinatura, 'ddmmyyyy') AS dataAssinatura,
                    TC.cod_modalidade_licitacao AS contDecLicitacao,
                    CASE WHEN TC.cod_modalidade_licitacao=5 OR TC.cod_modalidade_licitacao=6 THEN
                            (SELECT valor FROM administracao.configuracao_entidade
                            WHERE exercicio=TC.exercicio
                            AND parametro='tcemg_codigo_orgao_entidade_sicom'
                            AND cod_entidade=TC.cod_entidade_modalidade)
                    ELSE
                            ''
                    END AS codOrgaoResp,
                    CASE WHEN TC.cod_modalidade_licitacao=5 OR TC.cod_modalidade_licitacao=6 THEN
                            LPAD((LPAD(''||TC.num_orgao_modalidade,2, '0')||LPAD(''||TC.num_unidade_modalidade,2, '0')), 5, '0')
                    ELSE
                            LPAD((LPAD(''||TC.num_orgao,2, '0')||LPAD(''||TC.num_unidade,2, '0')), 5, '0')
                    END AS codUnidadeSubResp,
                    TC.nro_processo AS nroProcesso,
                    TC.exercicio_processo AS exercicioProcesso,
                    CASE WHEN TC.cod_modalidade_licitacao=3 OR TC.cod_modalidade_licitacao=6 THEN
                            TC.cod_tipo_processo::TEXT
                    ELSE
                            ''
                    END AS tipoProcesso,
                    TC.cod_objeto AS naturezaObjeto,
                    TC.objeto_contrato AS objetoContrato,
                    TC.cod_instrumento AS tipoInstrumento,
                    to_char(TC.data_inicio, 'ddmmyyyy') AS dataInicioVigencia,
                    to_char(TC.data_final, 'ddmmyyyy') AS dataFinalVigencia,
                    REPLACE(TC.vl_contrato::TEXT, '.', ',') AS vlContrato,
                    TC.fornecimento AS formaFornecimento,
                    TC.pagamento AS formaPagamento,
                    TC.execucao AS prazoExecucao,
                    TC.multa AS multaRescisoria,
                    TC.multa_inadimplemento,
                    TC.cod_garantia AS garantia,
                    (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm=TC.cgm_signatario) AS cpfSignatarioContratante,
                    to_char(TC.data_publicacao, 'ddmmyyyy') AS dataPublicacao,
                    sw_cgm.nom_cgm AS veiculoDivulgacao
                    FROM tcemg.contrato AS TC
                    INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = TC.numcgm_publicidade
     
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                    
                    ORDER BY TC.nro_contrato, TC.cod_entidade";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato11()
    {
        $stSql  = " SELECT 11 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    EITEM.cod_item AS codItem,
                    REPLACE(ROUND(EITEM.quantidade, 4)::TEXT, '.', ',') AS quantidadeItem,
                    REPLACE(ROUND((EITEM.vl_total/EITEM.quantidade), 4)::TEXT, '.', ',') AS valorUnitarioItem
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_empenho AS TCE
                    ON TCE.cod_contrato=TC.cod_contrato
                    AND TCE.exercicio=TC.exercicio
                    AND TCE.cod_entidade=TC.cod_entidade
                    INNER JOIN empenho.empenho AS EE
                    ON EE.exercicio=TCE.exercicio_empenho
                    AND EE.cod_entidade=TCE.cod_entidade
                    AND EE.cod_empenho=TCE.cod_empenho
                    INNER JOIN empenho.item_pre_empenho AS EITEM
                    ON EITEM.cod_pre_empenho=EE.cod_pre_empenho
                    AND EITEM.exercicio=EE.exercicio
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                    AND TC.cod_objeto!=4 AND TC.cod_objeto!=5
    
                    ORDER BY TC.nro_contrato, TC.cod_entidade";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato12(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato12()
    {
        $stSql  = " SELECT 12 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                            (SELECT valor FROM administracao.configuracao_entidade
                            WHERE exercicio=TC.exercicio
                            AND parametro='tcemg_codigo_orgao_entidade_sicom'
                            AND cod_entidade=TC.cod_entidade)
                    AS codOrgao,
                    LPAD((LPAD(''||TC.num_orgao,2, '0')||LPAD(''||TC.num_unidade,2, '0')), 5, '0') AS codUnidadeSub,
                    LPAD(''||OD.cod_funcao,2, '0') AS codfuncao,
                    OD.cod_subfuncao AS codsubfuncao,
                    (LPAD(''||	(SELECT num_programa FROM ppa.programa
                                    WHERE cod_programa=OP.cod_programa AND ativo=true LIMIT 1),4, '0'))
                    AS codprograma,
                    (LPAD(''||ACAO.num_acao,4, '0')) AS idacao,
                    ''::TEXT AS idsubacao,
                    (LPAD(''||REPLACE(OCD.cod_estrutural, '.', ''),6, '')) AS naturezadespesa,
                    recurso.cod_fonte AS codFontRecursos,
                    REPLACE(empenho.fn_consultar_valor_empenhado(
                            EE.exercicio
                            ,EE.cod_empenho
                            ,EE.cod_entidade)::TEXT, '.', ',')
                    AS vlRecurso
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_empenho AS TCE
                    ON TCE.cod_contrato=TC.cod_contrato
                    AND TCE.exercicio=TC.exercicio
                    AND TCE.cod_entidade=TC.cod_entidade
                    INNER JOIN empenho.empenho AS EE
                    ON EE.exercicio=TCE.exercicio_empenho
                    AND EE.cod_entidade=TCE.cod_entidade
                    AND EE.cod_empenho=TCE.cod_empenho
                    INNER JOIN empenho.pre_empenho AS EPE
                    ON EPE.cod_pre_empenho=EE.cod_pre_empenho
                    AND EPE.exercicio=EE.exercicio
                    INNER JOIN empenho.pre_empenho_despesa AS EPED
                    ON EPED.cod_pre_empenho=EPE.cod_pre_empenho
                    AND EPED.exercicio=EPE.exercicio
                    INNER JOIN orcamento.conta_despesa AS OCD
                    ON OCD.exercicio=EPED.exercicio
                    AND OCD.cod_conta=EPED.cod_conta
                    INNER JOIN orcamento.despesa AS OD
                    ON OD.exercicio=EPED.exercicio AND OD.cod_despesa=EPED.cod_despesa
                    INNER JOIN orcamento.programa AS OP
                    ON OP.cod_programa=OD.cod_programa
                    AND OP.exercicio=OD.exercicio
                    INNER JOIN orcamento.despesa_acao AS ODA
                    ON ODA.cod_despesa=OD.cod_despesa
                    AND ODA.exercicio_despesa=OD.exercicio
                    INNER JOIN ppa.acao AS ACAO
                    ON ACAO.cod_acao=ODA.cod_acao
                    INNER JOIN orcamento.recurso
                    ON recurso.exercicio=OD.exercicio
                    AND recurso.cod_recurso=OD.cod_recurso 
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                    AND TC.cod_objeto!=4 AND TC.cod_objeto!=5
                    
                    ORDER BY TC.nro_contrato, TC.cod_entidade";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato13.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato13(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato13()
    {
        $stSql  = " SELECT 13 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    CASE WHEN CGM.cod_pais!=1 THEN
                            3
                    WHEN CGMPJ.cnpj IS NOT NULL THEN
                            2
                    ELSE
                            1
                    END AS tipoDocumento,
                    CASE WHEN CGMPJ.cnpj IS NOT NULL THEN
                            CGMPJ.cnpj
                    ELSE
                            CGMPF.cpf
                    END AS nroDocumento,
                    (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm = TCF.cgm_representante) AS cpfRepresentanteLegal
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_fornecedor AS TCF
                    ON TCF.cod_contrato=TC.cod_contrato
                    AND TCF.exercicio=TC.exercicio
                    AND TCF.cod_entidade=TC.cod_entidade
                    LEFT JOIN sw_cgm_pessoa_juridica AS CGMPJ
                    ON CGMPJ.numcgm=TCF.cgm_fornecedor
                    LEFT JOIN sw_cgm_pessoa_fisica AS CGMPF
                    ON CGMPF.numcgm=TCF.cgm_fornecedor
                    INNER JOIN sw_cgm AS CGM
                    ON CGM.numcgm=TCF.cgm_fornecedor
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
    
                    ORDER BY TC.nro_contrato, TC.cod_entidade";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato20.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato20(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato20().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato20()
    {
        $stSql  = " SELECT 20 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    (TCA.exercicio||(LPAD(''||TCA.cod_entidade,2, '0'))
                    ||(LPAD(''||TCA.nro_aditivo,2, '0'))||(LPAD(''||TC.cod_contrato,3, '0'))
                    ||(LPAD(''||TC.cod_entidade,2, '0'))||(RIGHT (TC.exercicio, 2))) AS codAditivo,
                    (SELECT valor FROM administracao.configuracao_entidade
                                   WHERE exercicio=TCA.exercicio
                                   AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                   AND cod_entidade=TCA.cod_entidade) 
                    AS codOrgao,
                    LPAD((LPAD(''||TCA.num_orgao,2, '0')||LPAD(''||TCA.num_unidade,2, '0')), 5, '0') AS codUnidadeSub,
                    TC.nro_contrato AS nroContrato,
                    to_char(TC.data_assinatura, 'ddmmyyyy') AS dataAssinaturaContrato,
                    TCA.nro_aditivo AS nroAditivo,
                    to_char(TCA.data_assinatura, 'ddmmyyyy') AS dataAssinaturaAditivo,
                    TCA.cod_tipo_valor AS alteracaoValor,
                    LPAD(''||TCA.cod_tipo_aditivo,2, '0') AS tipoTermoAditivo,
                    TCA.descricao AS dscAlteracao,
                    to_char(TCA.data_termino, 'ddmmyyyy') AS novaDataTermino,
                    REPLACE(TCA.valor::TEXT, '.', ',') AS valorAditivo,
                    to_char(TCA.data_publicacao, 'ddmmyyyy') AS dataPublicacao,
                    sw_cgm.nom_cgm AS veiculoDivulgacao
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_aditivo AS TCA
                    ON TCA.cod_contrato=TC.cod_contrato
                    AND TCA.exercicio_contrato=TC.exercicio
                    AND TCA.cod_entidade_contrato=TC.cod_entidade
                    INNER JOIN sw_cgm
                    ON sw_cgm.numcgm=TCA.cgm_publicacao
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
    
                    ORDER BY TC.nro_contrato, TC.cod_entidade, TCA.nro_aditivo";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato21.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato21(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato21().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato21()
    {
        $stSql  = " SELECT 21 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    (TCA.exercicio||(LPAD(''||TCA.cod_entidade,2, '0'))
                    ||(LPAD(''||TCA.nro_aditivo,2, '0'))||(LPAD(''||TC.cod_contrato,3, '0'))
                    ||(LPAD(''||TC.cod_entidade,2, '0'))||(RIGHT (TC.exercicio, 2))) AS codAditivo,
                    EITEM.cod_item AS codItem,
                    CASE WHEN TCA.cod_tipo_aditivo=9 THEN
                            1
                    WHEN TCA.cod_tipo_aditivo=10 THEN
                            2
                    ElSE
                            TCAITEM.tipo_acresc_decresc
                    END AS tipoAlteracaoItem,
                    REPLACE(ROUND(TCAITEM.quantidade, 4)::TEXT, '.', ',') AS quantidade,
                    REPLACE(ROUND((EITEM.vl_total/EITEM.quantidade), 4)::TEXT, '.', ',') AS valorUnitario
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_aditivo AS TCA
                    ON TCA.cod_contrato=TC.cod_contrato
                    AND TCA.exercicio_contrato=TC.exercicio
                    AND TCA.cod_entidade_contrato=TC.cod_entidade
                    INNER JOIN tcemg.contrato_aditivo_item AS TCAITEM
                    ON TCAITEM.cod_contrato_aditivo=TCA.cod_contrato_aditivo
                    AND TCAITEM.exercicio=TCA.exercicio
                    AND TCAITEM.cod_entidade =TCA.cod_entidade
                    INNER JOIN empenho.item_pre_empenho AS EITEM
                    ON EITEM.exercicio=TCAITEM.exercicio_pre_empenho
                    AND EITEM.cod_pre_empenho=TCAITEM.cod_pre_empenho
                    AND EITEM.num_item=TCAITEM.num_item
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
    
                    ORDER BY TC.nro_contrato, TC.cod_entidade, TCA.nro_aditivo";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato30.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato30(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato30().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato30()
    {
        $stSql  = " SELECT 30 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    (SELECT valor FROM administracao.configuracao_entidade
                                   WHERE exercicio=TCA.exercicio
                                   AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                   AND cod_entidade=TCA.cod_entidade) 
                    AS codOrgao,
                    LPAD((LPAD(''||TC.num_orgao,2, '0')||LPAD(''||TC.num_unidade,2, '0')), 5, '0') AS codUnidadeSub,
                    TC.nro_contrato AS nroContrato,
                    to_char(TC.data_assinatura, 'ddmmyyyy') AS dataAssinaturaContrato,
                    LPAD(''||TCA.cod_tipo,2, '0') AS tipoApostila,
                    TCA.cod_apostila AS nroApostila,
                    to_char(TCA.data_apostila, 'ddmmyyyy') AS dataApostila,
                    TCA.cod_alteracao AS tipoAlteracaoApostila,
                    TCA.descricao AS dscAlteracao,
                    REPLACE(TCA.valor_apostila::TEXT, '.', ',') AS valorApostila
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_apostila AS TCA
                    ON TCA.cod_contrato=TC.cod_contrato
                    AND TCA.exercicio=TC.exercicio
                    AND TCA.cod_entidade=TC.cod_entidade
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
    
                    ORDER BY TC.nro_contrato, TC.cod_entidade";
                    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContrato40.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContrato40(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContrato40().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContrato40()
    {
        $stSql  = " SELECT 40 as tipoRegistro,
                    (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,9, '0'))) AS codContrato,
                    (SELECT valor FROM administracao.configuracao_entidade
                                   WHERE exercicio=TC.exercicio
                                   AND parametro='tcemg_codigo_orgao_entidade_sicom'
                                   AND cod_entidade=TC.cod_entidade) 
                    AS codOrgao,
                    LPAD((LPAD(''||TC.num_orgao,2, '0')||LPAD(''||TC.num_unidade,2, '0')), 5, '0') AS codUnidadeSub,
                    TC.nro_contrato AS nroContrato,
                    to_char(TC.data_assinatura, 'ddmmyyyy') AS dataAssinaturaContrato,
                    to_char(TCR.data_rescisao, 'ddmmyyyy') AS dataRescisao,
                    REPLACE(TCR.valor_rescisao::TEXT, '.', ',') AS valorRescisao
                    
                    FROM tcemg.contrato AS TC
                    INNER JOIN tcemg.contrato_rescisao AS TCR
                    ON TCR.cod_contrato=TC.cod_contrato
                    AND TCR.exercicio=TC.exercicio
                    AND TCR.cod_entidade=TC.cod_entidade
                    
                    WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND (TC.data_inicio <= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                        OR
                        TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    
                    AND (TC.data_final >= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        OR
                        TC.data_final BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        )--ENTRADA MES
                    AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
    
                    ORDER BY TC.nro_contrato, TC.cod_entidade";
                    
        return $stSql;
    }
	
	public function __destruct(){}

}
?>
