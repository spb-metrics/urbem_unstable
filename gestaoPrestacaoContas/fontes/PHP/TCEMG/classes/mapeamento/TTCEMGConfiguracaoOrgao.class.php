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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: TTCEMGConfiguracaoOrgao.class.php 61575 2015-02-10 12:53:21Z franver $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTCEMGConfiguracaoOrgao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGConfiguracaoOrgao()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configuracao_orgao');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,tipo_responsavel,num_cgm');
        
	$this->AddCampo('exercicio'              , 'varchar',   true,   '4',   true, false);
        $this->AddCampo('cod_entidade'           , 'integer',   true,    '',   true, false);
	$this->AddCampo('tipo_responsavel'       , 'integer',   true,    '',   true, false);
        $this->AddCampo('num_cgm'                , 'integer',   true,   '6',  false, false);
        $this->AddCampo('crc_contador'           , 'varchar',  false,  '11',  false, false);
        $this->AddCampo('uf_crcContador'         , 'varchar',  false,   '2',  false, false);
        $this->AddCampo('cargo_ordenador_despesa', 'varchar',  false,  '50',  false, false);
        $this->AddCampo('dt_inicio'              , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('dt_fim'                 , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('email'                  , 'varchar',  false,  '35',  false, false);
    }

    public function recuperaResponsaveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaResponsaveis().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    
    public function montaRecuperaResponsaveis()
    {
	$stSql = "
	SELECT configuracao_orgao.num_cgm
	     , cgm.nom_cgm
	     , CASE configuracao_orgao.tipo_responsavel WHEN 1 THEN 'Gestor'
							WHEN 2 THEN 'Contador'
							WHEN 3 THEN 'Controle Interno'
							WHEN 4 THEN 'Ordenador de Despesa por Delegação'
		END AS nom_tipo_responsavel
	     , TO_CHAR(configuracao_orgao.dt_inicio,'dd/mm/yyyy') AS dt_inicio
	     , TO_CHAR(configuracao_orgao.dt_fim,'dd/mm/yyyy') AS dt_fim
			     , configuracao_orgao.crc_contador
			     , configuracao_orgao.uf_crccontador
			     , configuracao_orgao.cargo_ordenador_despesa
			     , configuracao_orgao.email
			     , configuracao_orgao.tipo_responsavel
			     , configuracao_orgao.cod_entidade
	  FROM tcemg.configuracao_orgao
	  JOIN sw_cgm AS cgm
	    ON cgm.numcgm = configuracao_orgao.num_cgm
	 WHERE configuracao_orgao.exercicio = '".$this->getDado('exercicio')."'
	   AND configuracao_orgao.cod_entidade = ".$this->getDado('cod_entidade')."
	    ";
	    return $stSql;
    }
	
    public function recuperaCodigos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCodigos().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaCodigos()
    {
        $stSql = "  SELECT  ent.cod_entidade            
                           ,cgm.nom_cgm                 
                           ,ce.valor                    
                           ,( SELECT valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                 AND configuracao_entidade.parametro    = 'tcemg_codigo_tipo_balancete'
				 AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                           ) AS tipo_balancete         
                          ,( SELECT CASE WHEN LENGTH(valor) >= 11 
                                         THEN SUBSTR(valor, 1, LENGTH(valor)-11)
                                         ELSE valor
                                          END AS valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                 AND configuracao_entidade.parametro    = 'tcemg_tipo_orgao_entidade_sicom'
				 AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                            ) AS orgao_unidade          
                           ,( SELECT CASE WHEN LENGTH(valor) >= 11 
                                          THEN SUBSTR(valor, 1, LENGTH(valor)-11)
                                          ELSE valor
                                           END AS valor
                                FROM administracao.configuracao_entidade
                               WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                 AND configuracao_entidade.parametro    = 'tcemg_cgm_responsavel'
				 AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                            ) AS num_cgm          
                           ,(SELECT nom_cgm 
                               FROM sw_cgm 
                              WHERE numcgm = ( SELECT CASE WHEN LENGTH(valor) >= 11 
                                                           THEN SUBSTR(valor, 1, LENGTH(valor)-11) 
                                                           ELSE valor 
                                                           END AS valor
                                                 FROM administracao.configuracao_entidade
                                                WHERE configuracao_entidade.cod_entidade = ent.cod_entidade
                                                  AND configuracao_entidade.parametro    = 'tcemg_cgm_responsavel'
						  AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                                             )::integer
                            ) AS nom_cgm_responsavel
                      FROM sw_cgm AS cgm
		      
                INNER JOIN orcamento.entidade AS ent     
                        ON cgm.numcgm = ent.numcgm
			
                 LEFT JOIN administracao.configuracao_entidade AS ce      
                        ON ent.exercicio    = ce.exercicio           
                       AND ent.cod_entidade = ce.cod_entidade        
                       AND ce.parametro     = 'tcemg_codigo_orgao_entidade_sicom'
		       
                     WHERE ent.exercicio    = '".$this->getDado('exercicio')."'                  
                       AND ce.exercicio     = '".$this->getDado('exercicio')."'                              
                       AND ce.cod_modulo    = ".$this->getDado('cod_modulo')." \n";
		       
        return $stSql;
    }

    public function recuperaOrgao(&$rsRecordSet,$stFiltro = "",$stOrder = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaOrgao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgao()
    {
        $stSql  = "SELECT 10 AS tipoRegistro,
                          (SELECT valor::INTEGER
                             FROM administracao.configuracao_entidade
                            WHERE exercicio = ACE.exercicio
                              AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              AND cod_entidade = ACE.cod_entidade) AS codOrgao,
                          ACE.valor::INTEGER AS tipoOrgao,
                          CGM_PJ.cnpj::TEXT AS cnpjOrgao,
			  ACE.cod_entidade||''||ACE.exercicio AS chave
                    FROM administracao.configuracao_entidade AS ACE
              INNER JOIN orcamento.entidade AS OE
                      ON OE.cod_entidade = ACE.cod_entidade
                     AND OE.exercicio = ACE.exercicio
               LEFT JOIN sw_cgm_pessoa_juridica AS CGM_PJ
                      ON CGM_PJ.numcgm = OE.numcgm
                   WHERE ACE.exercicio = '".$this->getDado('exercicio')."'
                     AND ACE.cod_entidade IN (".$this->getDado('entidade').")
                     AND ACE.parametro = 'tcemg_tipo_orgao_entidade_sicom' ";
        return $stSql;
    }
    
    public function recuperaOrgao2015(&$rsRecordSet,$stFiltro = "",$stOrder = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaOrgao2015",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgao2015()
    {
        $stSql  = "SELECT 10 AS tipoRegistro,
                          (SELECT valor::INTEGER
                             FROM administracao.configuracao_entidade
                            WHERE exercicio = ACE.exercicio
                              AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                              AND cod_entidade = ACE.cod_entidade) AS codOrgao,
                          ACE.valor::INTEGER AS tipoOrgao,
                          CGM_PJ.cnpj::TEXT AS cnpjOrgao,
			  ACE.cod_entidade||''||ACE.exercicio AS chave,
			  CGM_fornecedor_sw.tipo_documento,
                          CGM_fornecedor_sw.numero_documento,
			  '".$this->getDado('versao')."'::text AS versao
                    FROM administracao.configuracao_entidade AS ACE
              INNER JOIN orcamento.entidade AS OE
                      ON OE.cod_entidade = ACE.cod_entidade
                     AND OE.exercicio = ACE.exercicio
               LEFT JOIN sw_cgm_pessoa_juridica AS CGM_PJ
                      ON CGM_PJ.numcgm = OE.numcgm
	      INNER JOIN ( SELECT valor 
			        , configuracao.exercicio
			        , CGM.tipo_documento
			        , CGM.numero_documento
		             FROM administracao.configuracao
                             JOIN ( SELECT sw_cgm.numcgm
					 , CASE WHEN sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm THEN 1
                                                WHEN sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm THEN 2
						ELSE 3
                                           END AS tipo_documento
			                 , CASE WHEN sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm THEN sw_cgm_pessoa_fisica.cpf
                                                WHEN sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm THEN sw_cgm_pessoa_juridica.cnpj
                                           END AS numero_documento
                                      FROM sw_cgm
                                 LEFT JOIN sw_cgm_pessoa_fisica
                                        ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                                 LEFT JOIN sw_cgm_pessoa_juridica
                                        ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                                  ) AS CGM
                                  ON CGM.numcgm::text = configuracao.valor
                               WHERE configuracao.parametro= 'fornecedor_software'
                                 AND configuracao.exercicio= '2015'
			) AS CGM_fornecedor_sw
		      ON CGM_fornecedor_sw.exercicio = '".$this->getDado('exercicio')."'      
                   WHERE ACE.exercicio = '".$this->getDado('exercicio')."'
                     AND ACE.cod_entidade IN (".$this->getDado('entidade').")
                     AND ACE.parametro = 'tcemg_tipo_orgao_entidade_sicom' ";
        return $stSql;
    }
    
    public function recuperaOrgaoResponsavel(&$rsRecordSet,$stFiltro = "",$stOrder = "",$boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaOrgaoResponsavel",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaOrgaoResponsavel()
    {
        
	$stSql = "SELECT 11 AS tipoRegistro
			, configuracao_orgao.tipo_responsavel AS tipoResponsavel
			, cgm_pf.rg AS cartIdent
			, sw_uf.sigla_uf AS orgEmissorCi
			, cgm_pf.cpf
			, CASE WHEN configuracao_orgao.tipo_responsavel = 2
			       THEN configuracao_orgao.crc_contador
			       ELSE ''
			  END as crcContador
			, CASE WHEN configuracao_orgao.tipo_responsavel = 2
			       THEN configuracao_orgao.uf_crccontador 
			       ELSE ''
			  END AS ufCrcContador
			, CASE WHEN configuracao_orgao.tipo_responsavel = 4
			       THEN configuracao_orgao.cargo_ordenador_despesa 
			       ELSE ''
			  END AS cargoOrdDespDeleg
			, configuracao_entidade.exercicio
			, configuracao_entidade.cod_entidade
			, configuracao_orgao.num_cgm AS cgm
			, CASE WHEN TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY'), 'yyyymmdd') > TO_CHAR(dt_inicio, 'yyyymmdd')
                   THEN TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY'), 'ddmmyyyy')
                   ELSE to_char(configuracao_orgao.dt_inicio, 'ddmmyyyy')
               END AS dtInicio
            , CASE WHEN TO_CHAR(TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY'), 'yyyymmdd') < TO_CHAR(dt_fim, 'yyyymmdd')
                   THEN TO_CHAR(TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY'), 'ddmmyyyy')
                   ELSE to_char(configuracao_orgao.dt_inicio, 'ddmmyyyy')
               END AS dtfinal
            , configuracao_orgao.email AS email
			, configuracao_entidade.cod_entidade||''||configuracao_entidade.exercicio AS chave
		   
		   FROM administracao.configuracao_entidade
	   
	     INNER JOIN tcemg.configuracao_orgao
		     ON configuracao_orgao.cod_entidade = configuracao_entidade.cod_entidade
		    AND configuracao_orgao.exercicio    = configuracao_entidade.exercicio
	    
	      LEFT JOIN sw_cgm_pessoa_fisica AS cgm_pf
		     ON cgm_pf.numcgm = configuracao_orgao.num_cgm
	 
	      LEFT JOIN sw_cgm AS cgm
		     ON cgm.numcgm = configuracao_orgao.num_cgm
	 
	     INNER JOIN sw_uf 
		     ON sw_uf.cod_uf = cgm_pf.cod_uf_orgao_emissor
	 
		  WHERE configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
		    AND configuracao_entidade.cod_entidade IN (".$this->getDado('entidade').")
		    AND configuracao_entidade.parametro    = 'tcemg_cgm_responsavel'
		    AND (TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY') BETWEEN dt_inicio AND dt_fim
                OR
				 TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY') BETWEEN dt_inicio AND dt_fim
				)
		";
	
        return $stSql;
    }
    
    function recuperaExportacaoOrgaoPlanejamento(&$rsRecordSet, $boTransacao = "")
    {
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaExportacaoOrgaoPlanejamento();
	$this->setDebug( $stSql);
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
	return $obErro;
    }
    
    function montaRecuperaExportacaoOrgaoPlanejamento()
    {
	$stSql = "
	    SELECT codigo_unidade_gestora.cod_orgao
		 , tipo_unidade_gestora.tipo_orgao
		 , responsavel_unidade_gestora.cpf
		 
	      FROM ( SELECT valor AS cod_orgao
			  , cod_entidade
		       FROM administracao.configuracao_entidade
		      WHERE parametro = 'tcemg_codigo_orgao_entidade_sicom'
			AND exercicio = '".Sessao::getExercicio()."'
		 ) AS codigo_unidade_gestora
	
	 LEFT JOIN ( SELECT valor AS tipo_orgao
			  , cod_entidade
		       FROM administracao.configuracao_entidade
		      WHERE parametro = 'tcemg_tipo_orgao_entidade_sicom'
			AND exercicio = '".Sessao::getExercicio()."'
		 ) AS tipo_unidade_gestora
		ON tipo_unidade_gestora.cod_entidade = codigo_unidade_gestora.cod_entidade
	
	 LEFT JOIN ( SELECT CGM_PF.cpf
			  , configuracao_orgao.cod_entidade  
		     FROM tcemg.configuracao_orgao
	       INNER JOIN sw_cgm_pessoa_fisica as CGM_PF
		       ON CGM_PF.numcgm = configuracao_orgao.num_cgm 
		      AND configuracao_orgao.tipo_responsavel = 1
		      AND configuracao_orgao.exercicio = '".Sessao::getExercicio()."'
		      
		    ) as responsavel_unidade_gestora
		   ON responsavel_unidade_gestora.cod_entidade = codigo_unidade_gestora.cod_entidade
	
		WHERE codigo_unidade_gestora.cod_entidade IN (".$this->getDado('entidade').") ";
    
	return $stSql;
    }
	
    public function __destruct(){}

}
?>
