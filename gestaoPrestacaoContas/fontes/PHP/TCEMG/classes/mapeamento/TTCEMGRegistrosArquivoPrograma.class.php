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
 * $Id: TTCEMGRegistrosArquivoPrograma.class.php 59719 2014-09-08 15:00:53Z franver $
 * $Revision: 59719 $
 * $Author: franver $
 * $Date: 2014-09-08 12:00:53 -0300 (Seg, 08 Set 2014) $
 * 
 */

class TTCEMGRegistrosArquivoPrograma extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('tcemg.registros_arquivo_programa');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_programa');

        $this->addCampo('cod_programa','integer'  ,true, '', true, true);
        $this->addCampo('timestamp'   ,'timestamp',true, '',false,false);
        $this->addCampo('exercicio'   ,'varchar'  ,true,'4', true,false);
    }
    
    public function recuperaTotalRecursos(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaTotalRecursos($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }

    private function montaRecuperaTotalRecursos($stFiltro = '', $stOrdem = '')
    {
        $stSql = "
            SELECT *
              FROM tcemg.recupera_ppa_programa('".$this->getDado('exercicio')."', ".$this->getDado('boReemissao').")
                AS retorno (
                            cod_programa INTEGER,
                            nome_programa VARCHAR,
                            objetivo VARCHAR,
                            total_recursos_ano_1 VARCHAR,
                            total_recursos_ano_2 VARCHAR,
                            total_recursos_ano_3 VARCHAR,
                            total_recursos_ano_4 VARCHAR
                            );
        ";
        return $stSql;
    }
    
    public function recuperaRecursosIncluisaoPrograma(&$rsRecordSet, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecursosIncluisaoPrograma($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }

    private function montaRecursosIncluisaoPrograma($stFiltro = '', $stOrdem = '')
    {
        $stSql = "
            SELECT *
              FROM tcemg.recupera_ppa_inclusao_programa('".$this->getDado('exercicio')."')
                AS retorno ( cod_programa        INTEGER
                           , nom_programa        VARCHAR
                           , objetivo            VARCHAR
                           , total_recurso_1_ano VARCHAR
                           , total_recurso_2_ano VARCHAR
                           , total_recurso_3_ano VARCHAR
                           , total_recurso_4_ano VARCHAR
                           , numero_lei          INTEGER
                           , data_lei            VARCHAR
                           , data_publicacao_lei VARCHAR
                           );
        ";
        return $stSql;
    }
    
    public function __destruct(){}


}
?>