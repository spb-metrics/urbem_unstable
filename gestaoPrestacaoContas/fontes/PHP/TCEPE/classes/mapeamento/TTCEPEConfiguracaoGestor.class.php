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

  * @author Analista: Silvia Martins
  * @author Desenvolvedor: Lisiane Morais

  * @ignore

  $Id: TTCEPEConfiguracaoGestor.class.php 60372 2014-10-16 12:02:07Z lisiane $
  $Date: $
  $Author: $
  $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEPEConfiguracaoGestor extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEPEConfiguracaoGestor()
    {
        parent::Persistente();
        $this->setTabela('tcepe.configuracao_gestor');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cgm_gestor,num_orgao, num_unidade ');
        
	$this->AddCampo('exercicio'              , 'varchar',   true,   '4',   true, false);
        $this->AddCampo('cod_entidade'           , 'integer',   true,    '',   true, false);
        $this->AddCampo('cgm_gestor'             , 'integer',   true,    '',  false, false);
        $this->AddCampo('num_orgao'              , 'integer',  false,    '',  false, false);
        $this->AddCampo('num_unidade'            , 'integer',  false,    '',  false, false);
        $this->AddCampo('dt_inicio_vigencia'     , 'date'   ,   true,    '',  false, false);
        $this->AddCampo('dt_fim_vigencia'        , 'date'   ,   true,    '',  false, false);
    }
    
    function recuperaExportacaoGestor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaExportacaoGestor().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaExportacaoGestor(){
	$stSql =  "  SELECT *
	               FROM
			(
			   SELECT configuracao_gestor.exercicio
				, cod_entidade
				, sw_cgm_pessoa_fisica.cpf
				, LPAD(CAST(configuracao_gestor.num_orgao AS VARCHAR),2,'0')||LPAD(CAST(configuracao_gestor.num_unidade AS VARCHAR),2,'0') AS unidade_orcamentaria
				, TO_CHAR(dt_inicio_vigencia, 'DDMMYYYY') AS dt_vigencia
				, 1 AS tipo_data
				  FROM tcepe.configuracao_gestor
			
			 INNER JOIN sw_cgm_pessoa_fisica 
				 ON sw_cgm_pessoa_fisica.numcgm = configuracao_gestor.cgm_gestor
			
			 INNER JOIN orcamento.unidade
				 ON unidade.exercicio   = configuracao_gestor.exercicio
				AND unidade.num_unidade = configuracao_gestor.num_unidade
				AND unidade.num_orgao   = configuracao_gestor.num_orgao
			      WHERE (
			             TO_DATE('".$this->getDado('data_inicial')."','DD/MM/YYYY') BETWEEN dt_inicio_vigencia AND dt_fim_vigencia
                                     OR
				     TO_DATE('".$this->getDado('data_final')."','DD/MM/YYYY') BETWEEN dt_inicio_vigencia AND dt_fim_vigencia
				    )
			
			UNION
			
			   SELECT configuracao_gestor.exercicio
				, cod_entidade
				, sw_cgm_pessoa_fisica.cpf
				, LPAD(CAST(configuracao_gestor.num_orgao AS VARCHAR),2,'0')||LPAD(CAST(configuracao_gestor.num_unidade AS VARCHAR),2,'0') AS unidade_orcamentaria
				, TO_CHAR(dt_fim_vigencia, 'DDMMYYYY') AS dt_vigencia
				, 2 AS tipo_data
				  FROM tcepe.configuracao_gestor
			
		       INNER JOIN sw_cgm_pessoa_fisica 
		       	       ON sw_cgm_pessoa_fisica.numcgm = configuracao_gestor.cgm_gestor
		       
		       INNER JOIN orcamento.unidade
			       ON unidade.exercicio   = configuracao_gestor.exercicio
			      AND unidade.num_unidade = configuracao_gestor.num_unidade
			      AND unidade.num_orgao   = configuracao_gestor.num_orgao
			  WHERE (
			         TO_DATE('".$this->getDado('data_inicial')."','DD/MM/YYYY') BETWEEN dt_inicio_vigencia AND dt_fim_vigencia
                                 OR
				 TO_DATE('".$this->getDado('data_final')."','DD/MM/YYYY') BETWEEN dt_inicio_vigencia AND dt_fim_vigencia
				)      
			) AS consulta
		    
		    WHERE TO_DATE(consulta.dt_vigencia, 'DDMMYYYY') <= TO_DATE('".$this->getDado('data_final')."','DD/MM/YYYY')
		      AND consulta.cod_entidade = ".$this->getDado('cod_entidade')."
		      AND consulta.exercicio    = '".$this->getDado('exercicio')."'
		 
		 ORDER BY consulta.dt_vigencia ";
	return $stSql;
    }

}
?>
